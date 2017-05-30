<?php
/**
 * Created by PhpStorm.
 * User: cristian
 * Date: 30.05.2017
 * Time: 16:06
 */

namespace AppBundle\Composer;

use Composer\Script\Event;

class ScriptHandler extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
{
    /**
     * Creates the sqlite database file
     */
    public static function databaseCreate(Event $event)
    {
        umask(0000);

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'database create');

        if (null === $consoleDir) {
            return;
        }

        static::executeCommand($event, $consoleDir, 'doctrine:database:create', $options['process-timeout']);
    }

    /**
     * Call the demo command of the Acme Demo Bundle.
     */
    public static function schemaUpdate(Event $event)
    {
        umask(0000);

        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'database create');

        if (null === $consoleDir) {
            return;
        }

        static::executeCommand($event, $consoleDir, 'doctrine:schema:update --force', $options['process-timeout']);
    }
}