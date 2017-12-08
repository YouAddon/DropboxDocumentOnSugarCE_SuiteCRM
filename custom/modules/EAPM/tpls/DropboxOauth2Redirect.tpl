<script>
    var message = {$response|@json};
    window.opener.postMessage(JSON.stringify(message), {$siteUrl|@json});
    window.close();
</script>