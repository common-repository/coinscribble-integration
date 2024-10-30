<?php

class Coinscribble_Integration_Migration
{
    public static function run_create() {
        $coinscribble_migrations = get_option('coinscribble_integration_migrations_executed', []);
        $migration_classes = ['Coinscribble_Integration_Transaction_Migration', 'Coinscribble_Integration_Add_Note_Column_Transaction_Migration'];
        foreach ($migration_classes as $class_name) {
            if (!in_array($class_name, $coinscribble_migrations)) {
                if (method_exists($class_name, 'run_create')) {
                    $result = $class_name::run_create();
                    if ($result['success']) {
                        $coinscribble_migrations[] = $class_name;
                    };
                }
            }
        }
        update_option('coinscribble_integration_migrations_executed', $coinscribble_migrations);
    }
}
