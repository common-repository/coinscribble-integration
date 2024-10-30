<?php

class Coinscribble_Integration_Error_Notification
{
    const transient_errors = 'Coinscribble_Integration_Error_Notification';

    public static function add_error(string $type, string $message)
    {
        $errors = get_option('Coinscribble_Integration_Error_Notification');
        $errors[$type] = $message;
        update_option(self::transient_errors, $errors);
    }

    public function notice()
    {
        $errors = get_option(self::transient_errors);

        if (!empty($errors)) {
            foreach ($errors as $error){
            ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($error) ?></p>
            </div>
            <?php
            }
        }
    }

    public static function clear_error(string $type_error )
    {
        $errors = get_option(self::transient_errors);
        if (!empty($errors[$type_error])) {
            unset($errors['$type_error']);
            update_option(self::transient_errors, $errors);
        }
    }

    public static function clear_all_errors()
    {
        delete_option(self::transient_errors);
    }
}
