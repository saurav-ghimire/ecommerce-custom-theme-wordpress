<?php

namespace Asylum\Common;

abstract class AbstractPostType
{
    protected $postType = '';

    protected $singular = '';

    protected $plural = '';

    abstract protected function args();

    public function register()
    {
        $this->args['labels'] = $this->labels();

        register_post_type($this->postType, $this->args());
    }

    public function labels()
    {
        if (!$this->singular) {
            $this->singular = substr($this->plural, 0, -1);
        }

        $labels = [
            'name'                  => ucfirst($this->plural),
            'singular_name'         => ucfirst($this->singular),
            'menu_name'             => ucfirst($this->plural),
            'name_admin_bar'        => ucfirst($this->singular),
            'add_new'               => "Add New",
            'add_new_item'          => "Add New " . ucfirst($this->singular),
            'new_item'              => "New " . ucfirst($this->singular),
            'edit_item'             => "Edit " . ucfirst($this->singular),
            'view_item'             => "View " . ucfirst($this->singular),
            'all_items'             => "All " . ucfirst($this->plural),
            'search_items'          => "Search " . ucfirst($this->plural),
            'parent_item_colon'     => "Parent " . ucfirst($this->plural) . ":",
            'not_found'             => "No " . $this->plural . " found.",
            'not_found_in_trash'    => "No " . $this->plural . " found in Trash.",
            'featured_image'        => ucfirst($this->singular) . " Cover Image",
            'set_featured_image'    => "Set cover image",
            'remove_featured_image' => "Remove cover image",
            'use_featured_image'    => "Use as cover image",
            'archives'              => ucfirst($this->singular) . " archives",
            'insert_into_item'      => "Insert into " . $this->singular,
            'uploaded_to_this_item' => "Uploaded to this " . $this->singular,
            'filter_items_list'     => "Filter " . $this->singular . " list",
            'items_list_navigation' => ucfirst($this->plural) . " list navigation",
            'items_list'            => ucfirst($this->plural) . " list",
        ];

        return $labels;

    }
}
