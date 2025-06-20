<?php
/**
 * 
 *  _____                                                                                _____ 
 * ( ___ )                                                                              ( ___ )
 *  |   |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|   | 
 *  |   |                                                                                |   | 
 *  |   |                                                                                |   | 
 *  |   |    ________  ___       __   _______   _______   ________                       |   | 
 *  |   |   |\   __  \|\  \     |\  \|\  ___ \ |\  ___ \ |\   ____\                      |   | 
 *  |   |   \ \  \|\  \ \  \    \ \  \ \   __/|\ \   __/|\ \  \___|_                     |   | 
 *  |   |    \ \  \\\  \ \  \  __\ \  \ \  \_|/_\ \  \_|/_\ \_____  \                    |   | 
 *  |   |     \ \  \\\  \ \  \|\__\_\  \ \  \_|\ \ \  \_|\ \|____|\  \                   |   | 
 *  |   |      \ \_____  \ \____________\ \_______\ \_______\____\_\  \                  |   | 
 *  |   |       \|___| \__\|____________|\|_______|\|_______|\_________\                 |   | 
 *  |   |             \|__|                                 \|_________|                 |   | 
 *  |   |    ________  ________  ________  _______   ________  ________  ________        |   | 
 *  |   |   |\   ____\|\   __  \|\   __  \|\  ___ \ |\   __  \|\   __  \|\   __  \       |   | 
 *  |   |   \ \  \___|\ \  \|\  \ \  \|\  \ \   __/|\ \  \|\  \ \  \|\  \ \  \|\  \      |   | 
 *  |   |    \ \  \    \ \  \\\  \ \   _  _\ \  \_|/_\ \   ____\ \   _  _\ \  \\\  \     |   | 
 *  |   |     \ \  \____\ \  \\\  \ \  \\  \\ \  \_|\ \ \  \___|\ \  \\  \\ \  \\\  \    |   | 
 *  |   |      \ \_______\ \_______\ \__\\ _\\ \_______\ \__\    \ \__\\ _\\ \_______\   |   | 
 *  |   |       \|_______|\|_______|\|__|\|__|\|_______|\|__|     \|__|\|__|\|_______|   |   | 
 *  |   |                                                                                |   | 
 *  |   |                                                                                |   | 
 *  |___|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|___| 
 * (_____)                                                                              (_____)
 * 
 * Эта программа является свободным программным обеспечением: вы можете распространять ее и/или модифицировать
 * в соответствии с условиями GNU General Public License, опубликованными
 * Фондом свободного программного обеспечения (Free Software Foundation), либо в версии 3 Лицензии, либо (по вашему выбору) в любой более поздней версии.
 *
 * @author TimQwees
 * @link https://github.com/TimQwees/Qwees_CorePro
 * 
 */

namespace App\Config;

use PDO;
use RuntimeException;
use App\Models\Network\Network;
class Database extends Network
{
    // Параметры подключения к базе данных
    private const DB_HOST = 'database';
    private const DB_PORT = '3306';
    private const DB_USERNAME = 'docker';
    private const DB_PASSWORD = 'docker';
    private const DB_NAME = 'docker';
    private static $instance = [];

    public static function getConnection()
    {
        if (empty(self::$instance)) {
            try {
                $options = [
                    PDO::ATTR_PERSISTENT => false, // Отключаем постоянное соединение
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_TIMEOUT => 10, // Увеличиваем таймаут
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4;
                        SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
                        SET SESSION innodb_lock_wait_timeout=10;
                        SET SESSION wait_timeout=120;
                        SET SESSION interactive_timeout=120;
                        SET SESSION net_read_timeout=60;
                        SET SESSION net_write_timeout=60;
                        SET SESSION max_execution_time=2000;
                        SET SESSION max_allowed_packet=16777216;
                        SET SESSION net_buffer_length=32768;"
                ];

                $dsn = "mysql:host=" . self::DB_HOST .
                    ";port=" . self::DB_PORT .
                    ";dbname=" . self::DB_NAME .
                    ";charset=utf8mb4" .
                    ";connect_timeout=10";

                self::$instance = new PDO($dsn, self::DB_USERNAME, self::DB_PASSWORD, $options);

                return self::$instance;
            } catch (\PDOException $e) {
                error_log("ошибка подключения к базе данных: " . $e->getMessage());
                self::onRedirect('/');//redirect to main page
                throw new RuntimeException('Ошибка подключения к базе данных. Пожалуйста, проверьте настройки.');
            }
        }

        return self::$instance;//PDO
    }

    public static function closeConnection()
    {
        self::$instance = [];
    }
}