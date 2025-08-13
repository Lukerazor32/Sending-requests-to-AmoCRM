<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/assets/js/validate.js"></script>
</head>
<body>
    <form id="order-form" action="../process_form.php" method="post">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" required>

        <label for="price">Цена:</label>
        <input type="number" id="price" name="price" required>
        <button type="submit">Отправить заявку</button>
    </form>
</body>
</html>