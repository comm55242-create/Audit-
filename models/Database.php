<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - DATABASE MODEL
// Securely encapsulates local Oracle connection logic.
// ==========================================================================

class Database {
    private static $conn = null;

    /**
     * Establishes or retrieves the active Oracle OCI connection.
     */
    public static function getConnection() {
        if (self::$conn === null) {
            $tns_local = '(DESCRIPTION =
                (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
                (CONNECT_DATA =
                  (SERVER = DEDICATED)
                  (SERVICE_NAME = orcl)
                )
              )';

            self::$conn = @oci_connect('SHOP', 'SHOP', $tns_local);
            
            if (!self::$conn) {
                throw new Exception("Database Connection failed: Could not connect to Oracle local listener on port 1521. Ensure local listener is active.");
            }
        }
        return self::$conn;
    }

    /**
     * Securely closes the active OCI connection.
     */
    public static function closeConnection() {
        if (self::$conn !== null) {
            @oci_close(self::$conn);
            self::$conn = null;
        }
    }
}
?>
