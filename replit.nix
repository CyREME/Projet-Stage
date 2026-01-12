{ pkgs }: {
	deps = [
   pkgs.tree
   pkgs.openssh_hpn
		pkgs.php82
	 pkgs.python3
	 pkgs.python3Packages.pip
	 pkgs.python3Packages.pdfplumber
	];
}