<?php/*
{
	"plugin": {
		"name": "JSONAPI",
		"type": "custom",
		"version": "1.0.0",
		"require": {
			"cms": "0.3.0"
		},
        "api_compatible": "1.0.0"
	},
    "global_config": {
        "servers": {
            "type": "group",
            "max": 10,
            "title": "@SERVER_NUMBER",
            "form": {
                "name": {
                    "type": "input",
                    "required": true,
                    "title": "@SERVER_NAME",
                    "placeholder": "Skyblock"
                },
                "ip": {
                    "type": "input",
                    "required": true,
                    "title": "@SERVER_IP",
                    "placeholder": "play.myserver.com"
                },
                "port": {
                    "type": "input",
                    "required": true,
                    "title": "@SERVER_PORT",
                    "tooltip": "@SERVER_PORT_INFO",
                    "placeholder": "25565",
                    "pattern": "[0-9]{2,5}",
                    "pattern_info": "@PATTERN_SERVER_PORT"
                },
                "username": {
                    "type": "input",
                    "required": true,
                    "title": "@USER"
                },
                "password": {
                    "type": "input",
                    "required": true,
                    "title": "@PASSWORD"
                }
            }
        }
    }
}
*/?>