<header class="header">
    <button id="sidePanelToggle" class="btn btn-dark">☰</button>
    <h1 class="header-title">My Medical Reports</h1>
</header>

<style>
    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: lightblue;
        color: white;
        padding: 10px 20px;
    }

    .header-title {
        margin: 0;
        text-align: center;

    }

    .btn {
        color: white;
        background-color: #495057;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 10px;
    }

    .btn:hover{
        background-color: #6c757d;
    }

</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = documnet.getElementById('sidePannelToggle');
        const sidePanel = document.getElementById('sidePanel');

        toggleButton.addEventListener('click', function(){
            if(sidePanel.style.left == "0px"){
                sidePanel.style.left = "-250px";
            }else{
                sidePanel.style.left = "0px";
            }
        });
    });
</script>