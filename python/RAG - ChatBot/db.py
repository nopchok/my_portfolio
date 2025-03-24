import sqlite3
import os

# Database file
DB_FILE = os.path.dirname(os.path.abspath(__file__)) + "\\DB.db"

    
def select_forum(data):
    try:
        # print(data)
        conn = sqlite3.connect(DB_FILE)
        cursor = conn.cursor()
        
        if( 'id' in data ):
            cursor.execute("SELECT * FROM forum WHERE id = ?;", (data['id'],))
            result = cursor.fetchall()
        else:
            cursor.execute("SELECT * FROM forum")
            result = cursor.fetchall()
            
        conn.close()
    except Exception as e:
        print(e)
        result = []
        pass
        
    return result
    
def insert_forum(data):
    try:
        # print('insert_data')
        conn = sqlite3.connect(DB_FILE)
        cursor = conn.cursor()
        
        cursor.execute('''
            INSERT OR IGNORE INTO forum (id, content, doctor_comments) VALUES (?, ?, ?);
        ''', (data['id'], data['content'], data['doctor_comments']))
        conn.commit()
        conn.close()
    except Exception as e:
        # print(e)
        pass
        
def migrate():
    """Run database migrations."""
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()

    # Create a migrations table if not exists
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL
        )
    ''')

    # Define migrations as a list of (name, SQL)
    migrations = [
        ("001_add_forum_table", '''
            CREATE TABLE IF NOT EXISTS forum (
                n INTEGER PRIMARY KEY AUTOINCREMENT,
                id INTEGER UNIQUE NOT NULL,
                content TEXT NULL,
                doctor_comments TEXT NULL
            );
        '''
        )
    ]

    for name, sql in migrations:
        # Check if the migration was already applied
        cursor.execute("SELECT 1 FROM migrations WHERE name = ?", (name,))
        if not cursor.fetchall():
            # print(f"Applying migration: {name}")
            cursor.execute(sql)
            cursor.execute("INSERT INTO migrations (name) VALUES (?)", (name,))
            conn.commit()
        else:
            # print(f"Skipping already applied migration: {name}")
            pass

    conn.close()

migrate()