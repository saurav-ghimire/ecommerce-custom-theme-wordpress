<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()) ?></h2>

    <p><?php echo esc_html__('Below are some common issues and steps to take to solve them.') ?></p>

    <p><?php echo esc_html__('Age Gate 3.x has many changes under the surface and while our testing has been pretty rigourous, every setup is different and thing may have slipped trhough the cracks.') ?></p>

    <div class="ag-accordion">
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('I can\'t get past the Age Gate / The Age Gate only worked once', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('The most likely cause for this is caching on your site either via a plugin or your hosting provider. If the Age Gate appears stuck try using the JavaScript mode in the advanced tab and clear any caches.', 'age-gate') ?></p>
        </div>

        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('I\'m seeing an API error', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('Age Gate 3 relies on the WordPress REST API when in JavaScript mode. It is common (and sensible) to lock down some of the API features. You you are doing so, make sure the Age Gate endpoints are allowed.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('My term or taxonomy isn\'t inheriting', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('The term you want to inherit must be resticted or bypassed depending on you mode. These settings are within the term edit screens. Age Gate will then use the highest restriction. For example, content has one unrestricted and one restricted term, the content will be restricted. Also, if you have Multi-ages enabled, the highest age will be used and an ae set on the content (ie, not the default age) always wins.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('I can\'t click my cookie banner or other elements', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('Age Gate utilises a "focus trap" to prevent users being able to access other parts of the site. This can also gobble up cookie banners and make them unresponsive. You can disable the focus trap in the advanced settings, or add elements to it using the age_gate/trap_focus/elements filter', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('My custom CSS is missing', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('The custom CSS editor is no longer permitted. On updating to version 3.x any custom style would have been moved to the default custom css editor in the Wordpress customiser. It was also moved to a custom option so you should be able to view it still in the Advanced settings - but note that content will not update.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('My custom CSS isn\'t working', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('Age Gate 3 takes a BEM approach to CSS selectors. If you upgraded from v2.x to 3, it should automatically be using the V2 selector, but check the CSS Style setting in the Advanced tab.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('My custom content has changed', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('The WYSIWYG editor has been removed in version 3 as it was generally problematic from a development point of view. The editor is now markdown based and much stricter on what is allowed. Shortcodes are still permitted.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('A hook has stopped working', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('Many hooks have been changed and/or removed in v3. Some critical ones have been back ported to still work with v3. Check the documentation for a full list of available hooks.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('Regions / Logs aren no longer working', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('Both the logs and regions extensions have had updates to run with v3. Ensure both are up to date. In some rare instance you may need to download the latest version again and upload it to your site.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('There\'s missing documentation', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('It\'s being worked on, but there are only so many hours in a day.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('Something else is not working', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('If something else isn\'t working, check the documentations first as there may be a solution. If not, raise a support question.', 'age-gate') ?></p>
        </div>
        <h3 class="ag-accordion__trigger ag-accordion__trigger--single"><?php echo esc_html__('Everything is broken!', 'age-gate') ?></h3>
        <div class="ag-accordion__body">
            <p><?php echo esc_html__('If something feels very off after a update there are a few steps you can take;', 'age-gate') ?></p>

            <h4><?php echo esc_html__('Non-destructive methods', 'age-gate'); ?></h4>
            <p><strong><?php echo esc_html__('Clear any caches.', 'age-gate') ?></strong> <?php echo esc_html__('This has been known to solve a lot of problems.', 'age-gate') ?></p>
            <p><strong><?php echo esc_html__('Try resaving the settings.', 'age-gate') ?></strong> <?php echo esc_html__('It could be something didn\'t update as expected.', 'age-gate') ?></p>

            <h4><?php echo esc_html__('More hardcore methods', 'age-gate') ?></h4>
            <p><?php echo esc_html__('Reinstall the plugin code.', 'age-gate') ?> <?php echo esc_html__('You can download the plugin and reupload to you site via either the Wordpress admin or SFTP.', 'age-gate') ?></p>
            <p><?php echo esc_html__('Reset the plugin.', 'age-gate') ?> <?php echo esc_html__('This will remove any settings and restore the default ones so use with caution.', 'age-gate') ?></p>

            <h4><?php echo esc_html__('Rollback the plugin') ?></h4>

            <p><?php echo sprintf(esc_html__('If all else fails, you can always roll back to a previous version of the plugin.', 'age-gate' )); ?>
            <p><a href="https://wordpress.org/plugins/age-gate/advanced/#download-previous-link" target="_blank" rel="noopener noreferrer"><?php echo esc_html__('See previous versions', 'age-gate') ?></a></p>
        </div>
    </div>
</div>
