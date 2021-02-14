--
-- Скрипт сгенерирован Devart dbForge Studio 2020 for MySQL, Версия 9.0.505.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 14.02.2021 21:16:37
-- Версия сервера: 5.7.31
-- Версия клиента: 4.1
--

-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установить режим SQL (SQL mode)
-- 
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

--
-- Установка базы данных по умолчанию
--
USE admin_uni;

--
-- Удалить таблицу `charts`
--
DROP TABLE IF EXISTS charts;

--
-- Удалить таблицу `charts_filters`
--
DROP TABLE IF EXISTS charts_filters;

--
-- Удалить таблицу `charts_presets`
--
DROP TABLE IF EXISTS charts_presets;

--
-- Установка базы данных по умолчанию
--
USE admin_uni;

--
-- Создать таблицу `charts_presets`
--
CREATE TABLE charts_presets (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать таблицу `charts_filters`
--
CREATE TABLE charts_filters (
  id int(11) NOT NULL AUTO_INCREMENT,
  posi tinyint(4) NOT NULL DEFAULT 0,
  chart_id int(11) NOT NULL DEFAULT 0,
  title varchar(255) NOT NULL,
  field varchar(255) NOT NULL,
  field_type enum ('bool', 'select', 'text') DEFAULT NULL,
  data_source text NOT NULL,
  default_value varchar(255) NOT NULL,
  `condition` varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 13,
AVG_ROW_LENGTH = 1365,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать таблицу `charts`
--
CREATE TABLE charts (
  id int(11) NOT NULL AUTO_INCREMENT,
  uniq_name varchar(255) NOT NULL COMMENT 'Уникальное имя графика, используется для холдера графика',
  title varchar(255) NOT NULL COMMENT 'Название графика',
  data_source varchar(255) NOT NULL COMMENT 'Имя таблицы - источник данных для графика',
  filters text NOT NULL COMMENT 'json массив фильтров применённые для графика',
  labels text NOT NULL COMMENT 'Лейблы, json для замены значений в group_field, лейблы отображаются в подписе к столбцам в графике',
  group_field varchar(255) NOT NULL COMMENT 'Поле для группировки, поле обязательное, по нему прозводится формирование стобцов графика',
  show_total_column tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Показать столбец "Всего" в графике',
  show_column_percents tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Показывать %ты в столбцах графика',
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 4,
AVG_ROW_LENGTH = 5461,
CHARACTER SET utf8,
COLLATE utf8_general_ci;

--
-- Создать индекс `uniq_name` для объекта типа таблица `charts`
--
ALTER TABLE charts
ADD UNIQUE INDEX uniq_name (uniq_name);

-- 
-- Вывод данных для таблицы charts_presets
--
-- Таблица admin_uni.charts_presets не содержит данных

-- 
-- Вывод данных для таблицы charts_filters
--
INSERT INTO charts_filters VALUES
(1, 1, 1, 'Партнеры', 'partner_id', 'select', '{"table":"partners","index":"id","value":"name"}', '', NULL),
(2, 2, 1, 'Пол', 'sex', 'select', '', '[{"id":"male","name":"Мужской"},{"id":"female","name":"Женский"}]', NULL),
(3, 3, 1, 'Возраст', 'age', 'text', '', '', NULL),
(4, 4, 1, 'Браузер', 'agent', 'text', '', '', NULL),
(5, 5, 1, 'ОС', 'os', 'text', ' ', '', NULL),
(6, 6, 1, 'Регионы', 'region', 'text', ' ', ' ', NULL),
(7, 7, 1, 'Разрешение экрана', 'screen', 'text', ' ', ' ', NULL),
(8, 8, 1, 'Пришел от партнера', 'came_from_partner', 'bool', ' ', ' ', NULL),
(9, 9, 2, 'Партнеры', 'partner_id', 'select', '{"table":"partners","index":"id","value":"name"}', ' ', ' '),
(10, 10, 3, 'Партнеры', 'partner_id', 'select', '{"table":"partners","index":"id","value":"name"}', '', ''),
(11, 11, 2, 'Пол', 'sex', 'select', '', '[{"id":"male","name":"Мужской"},{"id":"female","name":"Женский"}]', ' '),
(12, 12, 3, 'Пол', 'sex', 'select', '', '[{"id":"male","name":"Мужской"},{"id":"female","name":"Женский"}]', '');

-- 
-- Вывод данных для таблицы charts
--
INSERT INTO charts VALUES
(1, '60264573418be', 'Последние пользователи', '_charts_data_users', '[]', '{"complete_training":"Прошли обучение","left":"Ушли не зарег.","start_without_registration":"Начали без регистрации","potentially_new":"Потенциально новые"}', 'event', 1, 1),
(2, '983490anqi789', 'Последние пользователи 2', '_charts_data_users', '[]', '{"complete_training":"Прошли обучение","left":"Ушли не зарег.","start_without_registration":"Начали без регистрации","potentially_new":"Потенциально новые"}', 'event', 1, 1),
(3, '9801jp9ap123a', 'Последние пользователи 3', '_charts_data_users', '[]', '{"complete_training":"Прошли обучение","left":"Ушли не зарег.","start_without_registration":"Начали без регистрации","potentially_new":"Потенциально новые"}', 'event', 0, 0);

-- 
-- Восстановить предыдущий режим SQL (SQL mode)
--
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;