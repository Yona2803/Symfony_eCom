// document.addEventListener('DOMContentLoaded', function () {
//     console.log('DataTables initialized');
//     new DataTable('#example', {
//         ajax: {
//             url: '/users/customerslist',
//             method: 'GET',
//             dataSrc: ''
//         },
//         columns: [
//             { data: 'id' },
//             { data: 'firstName' },
//             { data: 'lastName' },
//             { data: 'email' },
//             { data: 'phone' }
//         ]
//     });
// });

new DataTable("#example", {
    ajax: {
        url: "/Users/customerslist",
        method: "GET",
        dataSrc: "",
    },
    columns: [
        { data: "id" },
        { data: "firstName" },
        { data: "lastName" },
        { data: "email" },
        { data: "phone" },
    ],
});
