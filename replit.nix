{ pkgs }: {
    deps = [
        # --- PHP ---
        pkgs.php
        pkgs.phpExtensions.mbstring
        pkgs.phpExtensions.pdo
        pkgs.phpExtensions.curl

        # --- PYTHON ---
        pkgs.python3
        pkgs.python3Packages.pip
        pkgs.python3Packages.pandas
        pkgs.python3Packages.openpyxl
        pkgs.python3Packages.psycopg2
        pkgs.python3Packages.pycryptodome
    ];
}