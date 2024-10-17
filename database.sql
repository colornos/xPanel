
---

### 3. `database.sql` (Database Setup Script)

```sql
CREATE DATABASE xpanel_db;

CREATE TABLE servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(255),
    user VARCHAR(255),
    password VARCHAR(255)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(255)
);
