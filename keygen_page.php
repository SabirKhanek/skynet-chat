<!DOCTYPE html>
<html>

<head>
    <title>Generate Key</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.2/axios.min.js" integrity="sha512-NCiXRSV460cHD9ClGDrTbTaw0muWUBf/zB/yLzJavRsPNUl9ODkUVmUHsZtKu17XknhsGlmyVoJxLg/ZQQEeGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>

<body>
    <main class="container">
        <h2>Generate license keys</h2>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" id="password" name="password">
            <small id="passwordHelp" class="form-text text-muted">Password that was set on server's env file. Default: (PASSKEY)</small>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control" type="text" id="name" name="name">
            <small id="nameHelp" class="form-text text-muted">Name of the license key</small>
        </div>
        <div class="form-group">
            <label for="license-key">License Key</label>
            <div class="input-group">
                <input type="text" class="form-control" id="license-key" name="license-key" disabled readonly>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="copy-btn">Copy</button>
                </div>
            </div>
            <small id="nameHelp" class="form-text text-muted">Press generate key button to generate the license key</small>
        </div>
        <button class="btn btn-primary" id="generate-btn">Generate</button>
    </main>
</body>
<script>
    document.getElementById('copy-btn').addEventListener('click', () => {
        // Get the text field
        var copyText = document.getElementById("license-key");

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        alert("Copied the text: " + copyText.value);
    });

    document.getElementById("generate-btn").addEventListener("click", function() {
        <?php echo "const host='" . get_option("local_python_server_host", "http://localhost:6929") . "';";
        ?>
        const password = document.getElementById("password").value;
        const name = document.getElementById("name").value;
        axios({
            method: 'post',
            url: host,
            data: {
                "auth": password,
                "name": name
            }
        }).then(function(response) {
            document.getElementById("license-key").value = response.data.key;
            alert('keygen success')
        }).catch(function(error) {
            console.log(error)
            alert('Error: ' + error.message + ` (${error.response.statusText})`)
        });
    });

    document.getElementById('name').addEventListener("keydown", async (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('generate-btn').click();
        }
    });
</script>

</html>