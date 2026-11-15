<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getPdo();

    echo "Applying Migration 04 (Supply Chain)...\n";
    if (file_exists(__DIR__ . '/migrations/04_supply_chain.sql')) {
        $db->exec(file_get_contents(__DIR__ . '/migrations/04_supply_chain.sql'));
        echo "Migration 04 applied!\n";
    }

    echo "Applying Seed 04 (Supply Chain)...\n";
    if (file_exists(__DIR__ . '/seeds/04_supply_chain_seed.sql')) {
        $db->exec(file_get_contents(__DIR__ . '/seeds/04_supply_chain_seed.sql'));
        echo "Seed 04 applied!\n";
    }

    echo "Applying Migration 05 (Accounting)...\n";
    if (file_exists(__DIR__ . '/migrations/05_accounting_module.sql')) {
        $db->exec(file_get_contents(__DIR__ . '/migrations/05_accounting_module.sql'));
        echo "Migration 05 applied!\n";
    }

    echo "Applying Seed 05 (Accounting)...\n";
    if (file_exists(__DIR__ . '/seeds/05_accounting_seed.sql')) {
        $db->exec(file_get_contents(__DIR__ . '/seeds/05_accounting_seed.sql'));
        echo "Seed 05 applied!\n";
    }

    echo "SUCCESS: All migrations & seeds applied successfully!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
