// Проверка времени
const startTime = Date.now();

function handleFormSubmit(event) {
    event.preventDefault();

    const timeSpentSec = Math.floor((Date.now() - startTime) / 1000);
    const spentTimeOver30 = timeSpentSec >= 30 ? 1 : 0;

    console.log(timeSpentSec);
    console.log(spentTimeOver30);

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    let phone = document.getElementById('phone').value;
    const price = document.getElementById('price').value;

    if (!name || !email || !phone || !price) {
        alert("Все поля обязательны для заполнения!");
        return;
    }

    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!emailRegex.test(email)) {
        alert("Введите корректный email!");
        return;
    }

    if (phone.length < 10) {
        alert("Телефон должен содержать хотя бы 10 символов!");
        return;
    }

    if (phone.length == 10) {
        phone = '+7' + phone;
    }
    let phoneRegex = /^(\+7|8)[0-9]{10}$/;
    if (!phoneRegex.test(phone)) {
        alert("Введите корректный российский номер телефона. Пример: +7 912 345 6789 или 8 912 345 6789");
        event.preventDefault(); // Останавливаем отправку формы
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('price', price);
    formData.append('spentTimeOver30Seconds', spentTimeOver30);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../process_form.php', true);

    xhr.onload = function () {
    if (xhr.status === 200) {
        alert("Форма успешно отправлена!");
        // document.getElementById('order-form').reset();
    } else {
        alert("Произошла ошибка при отправке формы!");
    }
    };

    xhr.send(formData);
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('order-form');
    form.addEventListener('submit', handleFormSubmit);
});