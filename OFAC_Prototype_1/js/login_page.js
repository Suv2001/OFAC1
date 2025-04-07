document.addEventListener('DOMContentLoaded', () => {
    const employeeId = document.querySelectorAll('.employeeId');
    const password = document.querySelectorAll('.password');
    const blocked = document.querySelectorAll('.blocked');
    console.log(Lockout);
    if (Lockout) {
        employeeId.forEach((element) => {
            element.style.display = 'none';
        });
        password.forEach((element) => {
            element.style.display = 'none';
        });
        blocked.forEach((element) => {
            element.style.display = 'block';
        });
    }
    else {
        employeeId.forEach((element) => {
            element.style.display = 'block';
        });
        password.forEach((element) => {
            element.style.display = 'block';
        });
        blocked.forEach((element) => {
            element.style.display = 'none';
        });
    }
});