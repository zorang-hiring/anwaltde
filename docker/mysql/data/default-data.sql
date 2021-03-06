CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE todo (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, due_on DATETIME DEFAULT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_5A0EB6A0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE todo ADD CONSTRAINT FK_5A0EB6A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);

INSERT INTO user (email, password) VALUES
    ('valid@email.com', '$2y$13$/IMKwcYRzsWyMDKCNQMIzeztxv8tzzsVrGb2ITeMIJsmYstiFljCa'),
    ('valid2@email.com', '$2y$13$oAshKu6bCtKdpVnnIcsI7.z7w.tKc5VLPi/.y4PjZNt5dFDHvV8SW');