-- Drop and recreate the database
DROP DATABASE IF EXISTS CPSC_431_HW2;
CREATE DATABASE IF NOT EXISTS CPSC_431_HW2;

-- Use the database first before creating tables
USE CPSC_431_HW2;

-- Create the TeamRoster table with UserAccount column
CREATE TABLE TeamRoster
( ID            INTEGER UNSIGNED  NOT NULL    AUTO_INCREMENT  PRIMARY KEY,
  Name_First    VARCHAR(100),
  Name_Last     VARCHAR(150)      NOT NULL,
  Street        VARCHAR(250),
  City          VARCHAR(100),
  State         VARCHAR(100),
  Country       VARCHAR(100),
  ZipCode       CHAR(10),
  UserAccount   VARCHAR(50)       DEFAULT NULL, -- Added column to link player to user account

    -- Zip code rules:
    --   5 digits, not all are zero and not all are nine,
    --   optionally followed by a hyphen and 4 digits, not all are zero and not all are nine.
  CHECK (ZipCode REGEXP '(?!0{5})(?!9{5})\\d{5}(-(?!0{4})(?!9{4})\\d{4})?'),

  INDEX  (Name_Last),
  UNIQUE (Name_Last, Name_First)
);

-- Create the Statistics table
CREATE TABLE Statistics
(
    ID                INTEGER    UNSIGNED  NOT NULL  AUTO_INCREMENT PRIMARY KEY,
    Player            INTEGER    UNSIGNED  NOT NULL,
    PlayingTimeMin    TINYINT(2) UNSIGNED  DEFAULT 0 COMMENT 'Two 20-minute halves',
    PlayingTimeSec    TINYINT(2) UNSIGNED  DEFAULT 0,
    Points            TINYINT    UNSIGNED  DEFAULT 0,
    Assists           TINYINT    UNSIGNED  DEFAULT 0,
    Rebounds          TINYINT    UNSIGNED  DEFAULT 0,

    FOREIGN KEY (Player) REFERENCES TeamRoster(ID) ON DELETE CASCADE,

    CHECK((PlayingTimeMin =  0             AND PlayingTimeSec BETWEEN 1 AND 59) OR
          (PlayingTimeMin BETWEEN 1 AND 39 AND PlayingTimeSec BETWEEN 0 AND 59) OR
          (PlayingTimeMin = 40             AND PlayingTimeSec = 0             ))
);

-- Create a view for players to see only their own stats
CREATE VIEW PlayerOwnStats AS
SELECT
    Statistics.ID,
    Statistics.Player,
    TeamRoster.Name_First,
    TeamRoster.Name_Last,
    Statistics.PlayingTimeMin,
    Statistics.PlayingTimeSec,
    Statistics.Points,
    Statistics.Assists,
    Statistics.Rebounds,
    TeamRoster.UserAccount
FROM Statistics
         JOIN TeamRoster ON Statistics.Player = TeamRoster.ID;

-- Create a view for players to see only their own address info
CREATE VIEW PlayerOwnInfo AS
SELECT
    ID,
    Name_First,
    Name_Last,
    Street,
    City,
    State,
    Country,
    ZipCode,
    UserAccount
FROM TeamRoster;

-- Create stored procedures for player to modify their own stats
DELIMITER //

-- Procedure for a player to insert their own statistics
CREATE PROCEDURE InsertPlayerStatistic(
    IN p_username VARCHAR(50),
    IN p_playingTimeMin TINYINT(2),
    IN p_playingTimeSec TINYINT(2),
    IN p_points TINYINT,
    IN p_assists TINYINT,
    IN p_rebounds TINYINT
        )
BEGIN
  DECLARE player_id INT;

  -- Get the player_id based on the username
SELECT ID INTO player_id
FROM TeamRoster
WHERE UserAccount = p_username;

IF player_id IS NOT NULL THEN
    INSERT INTO Statistics (
      Player,
      PlayingTimeMin,
      PlayingTimeSec,
      Points,
      Assists,
      Rebounds
    ) VALUES (
      player_id,
      p_playingTimeMin,
      p_playingTimeSec,
      p_points,
      p_assists,
      p_rebounds
    );
END IF;
END //

-- Procedure for a player to update their own statistics
CREATE PROCEDURE UpdatePlayerStatistic(
    IN p_username VARCHAR(50),
    IN p_stat_id INT,
    IN p_playingTimeMin TINYINT(2),
    IN p_playingTimeSec TINYINT(2),
    IN p_points TINYINT,
    IN p_assists TINYINT,
    IN p_rebounds TINYINT
        )
BEGIN
  DECLARE player_id INT;

  -- Get the player_id based on the username
SELECT ID INTO player_id
FROM TeamRoster
WHERE UserAccount = p_username;

IF player_id IS NOT NULL THEN
UPDATE Statistics SET
                      PlayingTimeMin = p_playingTimeMin,
                      PlayingTimeSec = p_playingTimeSec,
                      Points = p_points,
                      Assists = p_assists,
                      Rebounds = p_rebounds
WHERE ID = p_stat_id AND Player = player_id;
END IF;
END //

-- Procedure for a player to delete their own statistics
CREATE PROCEDURE DeletePlayerStatistic(
    IN p_username VARCHAR(50),
    IN p_stat_id INT
)
BEGIN
  DECLARE player_id INT;

  -- Get the player_id based on the username
SELECT ID INTO player_id
FROM TeamRoster
WHERE UserAccount = p_username;

IF player_id IS NOT NULL THEN
DELETE FROM Statistics
WHERE ID = p_stat_id AND Player = player_id;
END IF;
END //

-- Procedure for a player to update their own address
CREATE PROCEDURE UpdatePlayerAddress(
    IN p_username VARCHAR(50),
    IN p_street VARCHAR(250),
    IN p_city VARCHAR(100),
    IN p_state VARCHAR(100),
    IN p_country VARCHAR(100),
    IN p_zipcode CHAR(10)
)
BEGIN
UPDATE TeamRoster SET
                      Street = p_street,
                      City = p_city,
                      State = p_state,
                      Country = p_country,
                      ZipCode = p_zipcode
WHERE UserAccount = p_username;
END //

DELIMITER ;

-- Insert sample data with UserAccount field for testing player access
INSERT INTO TeamRoster VALUES
                           ('100', 'Donald',               'Duck',    '1313 S. Harbor Blvd.',    'Anaheim',            'CA',            'USA',     '92808-3232', 'Player'),
                           ('101', 'Daisy',                'Duck',    '1180 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830', NULL),
                           ('107', 'Mickey',               'Mouse',   '1313 S. Harbor Blvd.',    'Anaheim',            'CA',            'USA',     '92808-3232', NULL),
                           ('111', 'Pluto',                'Dog',     '1313 S. Harbor Blvd.',    'Anaheim',            'CA',            'USA',     '92808-3232', NULL),
                           ('118', 'Scrooge',              'McDuck',  '1180 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830', NULL),
                           ('119', 'Huebert (Huey)',       'Duck',    '1110 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830', NULL),
                           ('123', 'Deuteronomy (Dewey)',  'Duck',    '1110 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830', NULL),
                           ('128', 'Louie',                'Duck',    '1110 Seven Seas Dr.',     'Lake Buena Vista',   'FL',            'USA',     '32830', NULL),
                           ('129', 'Phooey',               'Duck',    '1-1 Maihama Urayasu',     'Chiba Prefecture',   'Disney Tokyo',  'Japan',   NULL, NULL),
                           ('131', 'Della',                'Duck',    '77700 Boulevard du Parc', 'Coupvray',           'Disney Paris',  'France',  NULL, NULL);

INSERT INTO Statistics VALUES
                           ('17', '100', '35', '12', '47', '11', '21'),
                           ('18', '107', '13', '22', '13', '01', '03'),
                           ('19', '111', '10', '00', '18', '02', '04'),
                           ('20', '128', '02', '45', '09', '01', '02'),
                           ('21', '107', '15', '39', '26', '03', '07'),
                           ('22', '100', '29', '47', '27', '09', '08');

-- Drop existing users if they exist
DROP USER IF EXISTS 'phpWebEngine'@'localhost';
DROP USER IF EXISTS 'Manager'@'localhost';
DROP USER IF EXISTS 'Coach'@'localhost';
DROP USER IF EXISTS 'Player'@'localhost';

-- Create the main application user (for backward compatibility)
CREATE USER 'phpWebEngine'@'localhost' IDENTIFIED BY '!_phpWebEngine';
GRANT SELECT, INSERT, DELETE, UPDATE, EXECUTE ON CPSC_431_HW2.* TO 'phpWebEngine'@'localhost';

-- Create the Manager user with full privileges
-- Manager can do whatever she wants
CREATE USER 'Manager'@'localhost' IDENTIFIED BY 'Manager_Pass123!';
GRANT ALL PRIVILEGES ON CPSC_431_HW2.* TO 'Manager'@'localhost';

-- Create the Coach user with specific privileges
-- Coach can maintain team roster (CRUD) and update player statistics (but not add/delete)
CREATE USER 'Coach'@'localhost' IDENTIFIED BY 'Coach_Pass123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON CPSC_431_HW2.TeamRoster TO 'Coach'@'localhost';
-- Coach can view and update specific columns in Statistics (but not add or delete)
GRANT SELECT ON CPSC_431_HW2.Statistics TO 'Coach'@'localhost';
GRANT UPDATE (PlayingTimeMin, PlayingTimeSec, Points, Assists, Rebounds) ON CPSC_431_HW2.Statistics TO 'Coach'@'localhost';

-- Create the Player user with limited privileges
-- Player can only maintain their own statistics and update their own address
CREATE USER 'Player'@'localhost' IDENTIFIED BY 'Player_Pass123!';
GRANT SELECT ON CPSC_431_HW2.TeamRoster TO 'Player'@'localhost';
GRANT SELECT ON CPSC_431_HW2.Statistics TO 'Player'@'localhost';
-- Player's ability to update only their own data will be handled through views and procedures

-- Grant EXECUTE privileges on stored procedures to the Player user
GRANT EXECUTE ON PROCEDURE CPSC_431_HW2.InsertPlayerStatistic TO 'Player'@'localhost';
GRANT EXECUTE ON PROCEDURE CPSC_431_HW2.UpdatePlayerStatistic TO 'Player'@'localhost';
GRANT EXECUTE ON PROCEDURE CPSC_431_HW2.DeletePlayerStatistic TO 'Player'@'localhost';
GRANT EXECUTE ON PROCEDURE CPSC_431_HW2.UpdatePlayerAddress TO 'Player'@'localhost';

-- Flush privileges to ensure changes take effect
FLUSH PRIVILEGES;