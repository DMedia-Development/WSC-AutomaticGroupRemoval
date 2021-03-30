DROP TABLE IF EXISTS wcf1_user_group_removal;
CREATE TABLE wcf1_user_group_removal (
	removalID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	groupID INT(10) NOT NULL,
	title VARCHAR(255) NOT NULL,
	isDisabled TINYINT(1) NOT NULL DEFAULT 0
);

ALTER TABLE wcf1_user_group_removal ADD FOREIGN KEY (groupID) REFERENCES wcf1_user_group (groupID) ON DELETE CASCADE;