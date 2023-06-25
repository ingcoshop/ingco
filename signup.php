<!DOCTYPE html>
<html>
<head>
    <title>Signup - My Website</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php require "h&f/header.php";?>

    <div style="margin: auto;max-width: 600px">

        <h2 style="text-align: center;">Sign Up</h2>

        <form method="post" action="database/signup_action.php" enctype="multipart/form-data" style="margin: auto;padding:10px;">

            <input type="text" name="username" placeholder="Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <div class="file-input-container">
                <label class="file-input-label">
                    <input type="file" class="file-input" name="image" accept="image/*" >
                </label>
            </div>
            <div class="file-input-preview">
                <img id="preview-image" src="images/default.png" alt="Preview Image">
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>
    <?php require "h&f/footer.php";?>
    <script>
        const fileInput = document.querySelector('.file-input');
        const previewImage = document.querySelector('#preview-image');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.addEventListener('load', function() {
                    previewImage.setAttribute('src', this.result);
                });
                reader.readAsDataURL(file);
            } else {
                previewImage.setAttribute('src', '#');
            }
        });
    </script>

