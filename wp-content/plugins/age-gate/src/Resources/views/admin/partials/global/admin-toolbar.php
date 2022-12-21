<div class="age-gate-toolbar">

    <h2 class="age-gate-toolbar__title"><i class="age-gate-toolbar__icon dashicons dashicons-lock"></i> <?php echo esc_html(__('Age Gate', 'age-gate')) ?></h2>

    <ul class="age-gate-toolbar__tabs">
        <?php foreach ($tabs ?? [] as $slug => $name) : ?>
            <li class="age-gate-toolbar__tab<?php echo ($slug === sanitize_text_field($_GET['page']) ?? false) ? ' age-gate-toolbar__tab--active' : '' ?>">
                <a href="<?php echo esc_url(add_query_arg(['page' => esc_attr($slug)], admin_url('admin.php'))) ?>" class="age-gate-toolbar__link"><?php echo esc_html($name) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <button class="age-gate-toolbar__button age-gate-toolbar__button--hidden">More</button>

    <ul class="age-gate-toolbar__extra age-gate-toolbar__extra--hidden"></ul>
</div>
