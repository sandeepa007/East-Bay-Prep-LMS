<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

function getUserProfileURL($userId = '', $with_a = true, $default = '&ndash;')
{
    $url = $default;

    $user_info = get_userdata($userId);

    if ($user_info) {
        $edit_link = get_edit_user_link($userId);
        $url = $with_a ? '<a class="mucp_username_redirection" href="'.esc_url($edit_link).'">'.$user_info->user_login.'</a>' : $edit_link;
    }

    return apply_filters('mucp_user_profile_url', $url, $userId, $with_a, $default);
}
