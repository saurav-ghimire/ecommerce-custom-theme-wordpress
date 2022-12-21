<?php

namespace AgeGate\Common;

use WP_Post;
use WP_Term;
use WP_User;
use WP_Post_Type;
use AgeGate\Common\Status;
use Asylum\Utility\Language;
use AgeGate\Common\Helpers\Woocommerce;

class Content
{
    protected $object = null;

    protected $type = null;

    protected $bypass = false;

    protected $age = false;

    protected $restrict = false;

    protected $id = null;

    protected $slug = null;

    protected $passed = false;

    protected $restricted = '';

    protected $terms = false;

    protected $language = '';

    protected $defaultAge = null;

    protected $realAge = null;

    public function __construct($id = null, $type = 'post')
    {
        if (!$id) {
            $this->object = get_queried_object();
        } else {
            switch ($type) {
                case 'term':
                case 'category':
                case 'post_tag':
                    $this->object = get_term($id);
                    break;
                case 'user':
                case 'author':
                    $this->object = get_user_by('id', $id);
                    break;
                default:
                    $this->object = get_post($id);
            }
        }


        $this->setType();
        $this->setLanguage();
        $this->setBypass();
        $this->setRestrict();
        $this->setDefaultAge();
        $this->setAge();

        $this->setStatus();
    }

    private function getItem($item)
    {
        $key = sprintf('_age_gate-%s', $item);
        switch ($this->type) {
            case 'post':
            case 'user':
            case 'term':
                return call_user_func('get_' . $this->type . '_meta', $this->id, $key, true) ?: false;
            case 'post_type':
            case 'archive':
                return Settings::getInstance()->archives[$this->slug][$item] ?? false;
            case 'error':
                return false;
        }
    }

    public function getArchiveType()
    {
        if (!is_home() && !is_archive() && !is_search()) {
            return false;
        }

        $archives = [
            'search',
            'month',
            'home',
            'year',
            'day',
        ];

        foreach ($archives as $archive) {
            $test = 'is_' . $archive;
            if ($test()) {
                return $archive;
            }
        }

        return 'archive';
    }

    public function setType($object = null)
    {
        $object = $object ?: $this->object;

        if ($object instanceof WP_Post) {
            $this->type = 'post';
            $this->setId($object->ID);
            $this->setSlug($object->post_name);
            $this->setTerms();
        } elseif ($object instanceof WP_User) {
            $this->type = 'user';
            $this->setId($object->ID);
            $this->setSlug($object->data->user_nicename);
        } elseif ($object instanceof WP_Term) {
            $this->type = 'term';
            $this->setId($object->term_id);
            $this->setSlug($object->slug);
        } elseif ($object instanceof WP_Post_Type) {
            if (Woocommerce::isShop() !== false) {
                $this->type = 'post';
                $this->object = get_post(Woocommerce::isShop());
                $this->setId($this->object->ID);
                $this->setSlug($this->object->post_name);
            } else {
                $this->type = 'post_type';
                $this->setSlug($object->name);
            }
        } elseif (is_404()) {
            $this->type = 'error';
        } elseif ($object === 'shortcode') {
            $this->type = 'shortcode';
        } else {
            $this->type = 'archive';
            $this->setSlug($this->getArchiveType());
        }

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setTerms()
    {
        $settings = Settings::getInstance();

        if (!$settings->inherit ?? false) {
            return;
        }

        $this->terms = collect(get_object_taxonomies($this->object->post_type))
                ->mapWithKeys(function ($item) {
                    return [
                        $item => collect(get_the_terms($this->id, $item) ?: [])->map(function ($term) {
                            if (!$this->inherited($term->term_id)) {
                                return false;
                            } else {
                                $meta = get_term_meta($term->term_id);
                                $term->age = (int) ($meta['_age_gate-age'][0] ?? false);
                                $term->restrict =  $meta['_age_gate-restrict'][0] ?? false;
                                $term->bypass =  $meta['_age_gate-bypass'][0] ?? false;
                                return $term;
                            }
                        })->filter()->all()
                    ];
                })
                ->flatten()
                ->all();

        return $this;
    }

    public function getTerms()
    {
        return $this->terms;
    }

    public function inherited(int $id)
    {
        $settings = Settings::getInstance();
        return $settings->inherit &&
            array_key_exists($id, $settings->terms ?? []) &&
            in_array($this->object->post_type, $settings->terms[$id] ?? []);
    }


    /**
     * @param mixed $object
     * @return void
     */
    public function getProperty($prop)
    {
        return $this->object->$prop ?? false;
    }

    /**
     * Get the value of bypass
     */
    public function getBypass()
    {
        return $this->bypass;
    }

    /**
     * Set the value of bypass
     *
     * @return  self
     */
    public function setBypass($bypass = false)
    {
        if ($bypass) {
            $this->bypass = $bypass;
            return $this;
        }

        if ($this->type === 'post' && Settings::getInstance()->inherit) {
            $bypass = collect($this->terms)->where('bypass', "1")->all() ? true : false;
        }

        $this->bypass = (bool) $this->getItem('bypass') ?: $bypass;

        return $this;
    }

    /**
     * Get the value of restrict
     */
    public function getRestrict()
    {
        return $this->restrict;
    }

    /**
     * Set the value of restrict
     *
     * @return  self
     */
    public function setRestrict(bool $restrict = false)
    {
        if ($restrict) {
            $this->restrict = $restrict;
            return;
        }

        if ($this->type === 'post' && Settings::getInstance()->inherit) {
            $restrict = collect($this->terms)->where('restrict', "1")->all() ? true : false;
        }

        $this->restrict = (bool) $this->getItem('restrict') ?: $restrict;

        return $this;
    }

    /**
     * Set the default age accounting for
     * multi region sites
     *
     * @return self
     */
    public function setDefaultAge($age = null)
    {
        if ($age) {
            $this->defaultAge = $age;
            return $this;
        }

        $settings = Settings::getInstance();

        $this->defaultAge = is_admin() ? $settings->{$this->language}['defaultAge'] ?? $settings->defaultAge : $settings->defaultAge;
        return $this;
    }

    /**
     * Get the value of age
     */
    public function getAge($display = false)
    {
        if ($display) {
            return $this->realAge;
        }
        return $this->age;
    }

    /**
     * Set the value of age
     *
     * @return  self
     */
    public function setAge($age = null)
    {
        $settings = Settings::getInstance();

        if ($age) {
            $this->age = apply_filters('age_gate/content/age', $age, $settings);

            if ($settings->anonymous) {
                $this->realAge = $this->age;
                $this->age = 1;
                return $this;
            }

            return $this;
        }

        if (!$settings->multiAge) {
            if (is_admin()) {
                $this->age = $settings->{$this->language}['defaultAge'] ?? $settings->defaultAge;
            } else {
                $this->age = apply_filters('age_gate/content/age', $settings->defaultAge, $settings);

                if ($settings->anonymous) {
                    $this->realAge = $this->age;
                    $this->age = 1;
                    return $this;
                }
            }
            return $this;
        }

        $ages = [
            $this->defaultAge
        ];

        if ($postAge = $this->getItem('age')) {
            $ages = [(int) $postAge];
        }

        // post age takes priority
        if ($settings->inherit && $this->terms && !$postAge) {
            $ages[] = collect($this->terms)->pluck('age')->max();
        }

        $ages = apply_filters('age_gate/content/ages', $ages, $settings);

        $this->age = max($ages);


        if ($settings->anonymous) {
            $this->realAge = $this->age;
            $this->age = 1;
            return $this;
        }


        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set the value of object
     *
     * @return  self
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get object language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus()
    {
        $settings = Settings::getInstance();

        if ($settings->isBuilder) {
            return $this;
        }

        $status = new Status($this);

        $this->restricted = $status->getRestricted();
        $this->passed = $status->getPassed();

        return $this;
    }

    public function isRestricted()
    {
        if (!$this->restricted) {
            return false;
        }

        return !$this->passed;
    }

    public function getRestricted()
    {
        return $this->restricted;
    }

    public function setLanguage()
    {
        $language = Language::getInstance();

        if ($language->multilingual()) {
            if (!is_admin()) {
                $this->language = $language->getLanguage();
                return $this;
            }


            $this->language = $language->getObjectLanguage($this->id, $this->type);

            return $this;
        }

        $this->language = $language->getLanguage();
        return $this;
    }
}
