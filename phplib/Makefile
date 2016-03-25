# Description: Makefile


CONF_DIR=/home/work/webroot/phpsrc/conf/phplib
SRC_DIR=/home/work/webroot/phpsrc/phplib


TIME=`date +%Y%m%d%H%M`

rd22:
	@rm -rf output
	ssh work@$(RD22) "mv $(CONF_DIR) $(CONF_DIR).$(TIME); mkdir -p $(CONF_DIR); cp -r $(CONF_DIR).$(TIME)/* $(CONF_DIR)/;  mv $(SRC_DIR) $(SRC_DIR).$(TIME); mkdir -p $(SRC_DIR);"
	scp -r * work@$(RD22):$(SRC_DIR)
	scp -r config/*.php work@$(RD22):$(CONF_DIR)
	@ssh work@$(RD22) "cd $(SRC_DIR); rm -rf config; find ./ -type d -name .svn|xargs -i rm -rf {}; cp use_offline_config.sh $(CONF_DIR); cd $(CONF_DIR); find ./ -type d -name .svn|xargs -i rm -rf {}; sh use_offline_config.sh;"
