<?php

return [
    "CREATE TABLE index_metadata (indexer_code VARCHAR(255) NOT NULL DEFAULT '', UNIQUE KEY (indexer_code));",
    "CREATE TABLE some_entity (entity_id INT NOT NULL, name VARCHAR(255) DEFAULT '', PRIMARY KEY (entity_id));",
];
