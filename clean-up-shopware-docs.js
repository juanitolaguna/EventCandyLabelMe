// ==UserScript==
// @name         Clean Up Shopware Docs
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Make the shopwre docs more readable on small notebook displays, turn of the sidebars.
// @author       You
// @match        https://docs.shopware.com/*
// @grant        nones
// ==/UserScript==

(function() {
    'use strict';

    createRightSidebarButton();
    createLeftSidebarButton();

    let content = document.getElementsByClassName('wiki-content--entry')[0];
    content.setAttribute("style","max-width:none;");
    removeAnouncment();

    function removeAnouncment() {
        const banner = document.getElementsByClassName('announcement--banner')[0];
        banner.style.display = "none";
    }


    // Your code here...
    function createLeftSidebarButton() {
        let el = document.getElementsByClassName('wiki-header--container')[0];
        const button = createButton('Sidebar Left');
        el.appendChild(button);
        button.addEventListener('click', closeSbRight);
    }

    function createRightSidebarButton() {
        let el = document.getElementsByClassName('wiki-header--container')[0];
        const button = createButton('Navigation');
        el.appendChild(button);
        button.addEventListener('click', closeSbLeft);
    }



    function createButton(btnText) {
        let btn = document.createElement('span');
        btn.className += "button mt1 mt0-s ml0 ml1-s";
        btn.setAttribute("style", "padding:5px; border: 1px solid #607182; border-radius: 5px; cursor: pointer;");

        let text = document.createTextNode(btnText);
        btn.appendChild(text);
        return btn;
    }

    function closeSbRight() {
        const sbRight = document.getElementsByClassName('entry--details')[0];
        _toggleShowHideRight(sbRight);
    }

    function closeSbLeft() {
        const sbLeft = document.getElementsByClassName('wiki--aside')[0];
        _toggleShowHideLeft(sbLeft);
    }

    function _toggleShowHideLeft(x) {
        const el = document.getElementsByClassName('page-wrap')[0];
        const footer = document.getElementsByClassName('footer--container')[0];

        if (x.style.display === "none") {
            x.style.display = "block";
            footer.style.display = "block"
            el.setAttribute("style", "padding-left: 300px");
        } else {
            x.style.display = "none";
            footer.style.display = "none";
            el.setAttribute("style", "padding-left: 0px");
        }
    }

    function _toggleShowHideRight(x) {
        const el = document.getElementsByClassName('wiki--content')[0];
        if (x.style.display === "none") {
            x.style.display = "block";
            el.setAttribute("style", "padding-right: 400px");
        } else {
            x.style.display = "none";
            el.setAttribute("style", "padding-right: 100px");
        }
    }
})();
