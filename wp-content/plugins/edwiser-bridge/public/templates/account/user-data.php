
<section class="eb-user-info eb-edit-user-wrapper">
    <aside class="eb-user-picture">
        <?php echo $user_avatar; ?>
    </aside>
    <div class="eb-user-data">
        <?php
        printf(esc_attr__('Hello %s%s%s (not %2$s? %sSign out%s)', 'eb-textdomain'), '<strong>', esc_html($user->display_name), '</strong>', '<a href="' . esc_url(wp_logout_url(get_permalink())) . '">', '</a>');
        ?>
        <div>
            <?php
            $userInfo = get_userdata(get_current_user_id());
            echo $userInfo->description;
            ?>
        </div>
        <div>
            Available Credit: <?php echo do_shortcode('[uw_balance display_username="false" separator=" " username_type="display_name"]'); ?>
        </div>    
        <div>
                <?php echo do_shortcode('[uw_product_table]'); ?>
        </div>
    </div>
</section>
