<?php
session_start();
?>
<button onclick="toggleOn()">Verlanglijst</button>

<div id="wishlist-overlay">
    <div id="wishlist">

        <div id="verlanglijst-namen">
            <ul>
                <li></li>
            </ul>
        </div>

        <div id="producten">
            <button onclick="toggleOff()">X</button>
        </div>    

    </div>
</div>

<script>
    function toggleOn() {
        document.getElementById("wishlist-overlay").style.display = "block";
    }

    function toggleOff() {
        document.getElementById("wishlist-overlay").style.display = "none";
    }
</script>

<style>
    #wishlist-overlay {
        position: fixed;
        /*display: none;*/
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
    }
    #wishlist {
        position: absolute;
        width: 50%;
        height: 75%;
        margin: auto;
        top: 15%;
        left: 25%;
        background-color: black;
        color: white;
        border: 2px solid;
        box-shadow: 0 4px 8px 0 rgba(111, 65, 148, 2), 0 6px 20px 0 rgba(111, 65, 148, 1);
    }
    #verlanglijst-namen {
        width: 25%;
        height: 100%;
        overflow: auto;
        background-color: blue;
        float: left;
        border-right: 1px solid;
    }
    #producten {
        width: 74%;
        height: 100%;
        overflow: auto;
        background-color: red;
        float: right;
        position: relative;
        border-left: 1px solid;
    }
    #producten button {
        position: absolute;
        top: 0;
        right: 0;
        background-color: #c9c9c9;
    }
    #producten button:hover{
        cursor: pointer;
        background-color: #b8b8b8;
    }
</style>