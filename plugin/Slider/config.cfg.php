<?php/*
{
	"plugin": {
		"name": "Slider",
		"type": "header",
		"version": "1.0.0",
		"require": {
			"cms": "0.3.0",
			"template": "1.0.0"
		}
	},
	"lang": {
		"translation": true,
		"default_language": "fr_FR"
	},
	"config": {
        "slides": {
            "type": "group",
            "max": 10,
            "title": "@SLIDE_NUMBER",
            "form": {
        		"title": {
        			"type": "input",
        			"title": "@SLIDE_TITLE",
        			"required": true,
        			"placeholder": "@SLIDE_NEEDED"
        		},
        		"desc": {
        			"title": "@SLIDE_DESC",
        			"type": "input",
        			"placeholder": "@SLIDE_EMPTY_ALLOWED"
        		},
        		"image": {
        			"title": "@SLIDE_IMAGE",
        			"type": "select",
        			"choices": {
        				"1": "{@SLIDE_PICTURE}1",
        				"2": "{@SLIDE_PICTURE}2",
        				"3": "{@SLIDE_PICTURE}3",
        				"4": "{@SLIDE_PICTURE}4",
        				"5": "{@SLIDE_PICTURE}5",
        				"6": "{@SLIDE_PICTURE}6",
        				"7": "{@SLIDE_PICTURE}7",
        				"8": "{@SLIDE_PICTURE}8",
        				"9": "{@SLIDE_PICTURE}9"
        			},
        			"default": "1"
        		}
            }
        }
	}
}
*/?>