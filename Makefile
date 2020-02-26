database:
	bin/console d:d:drop --force  --if-exists
	bin/console d:d:c
	bin/console d:m:m -n
	bin/console d:f:l -n
