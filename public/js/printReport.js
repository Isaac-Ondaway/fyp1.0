// // printReport.js

// // Function to preview the PDF
// function previewPDF() {
//     const { jsPDF } = window.jspdf;
//     const doc = new jsPDF();

//     doc.text("Batch Intake Report", 10, 10); // Example title
//     doc.html(document.querySelector('#reportSection'), {
//         callback: function (doc) {
//             const pdfBlob = doc.output('blob');
//             const pdfUrl = URL.createObjectURL(pdfBlob);

//             // Open the PDF in a new tab
//             window.open(pdfUrl, '_blank');
//         },
//         x: 10,
//         y: 20,
//     });
// }

// // Function to print the PDF
// function printPDF() {
//     const { jsPDF } = window.jspdf;
//     const doc = new jsPDF();

//     doc.text("Batch Intake Report", 10, 10); // Example title
//     doc.html(document.querySelector('#reportSection'), {
//         callback: function (doc) {
//             doc.save('Batch_Intake_Report.pdf'); // Save the file as PDF
//         },
//         x: 10,
//         y: 20,
//     });
// }

// // Attach event listeners (if required)
// document.addEventListener('DOMContentLoaded', function () {
//     const previewButton = document.getElementById('previewPDF');
//     const printButton = document.getElementById('printPDF');

//     if (previewButton) {
//         previewButton.addEventListener('click', previewPDF);
//     }

//     if (printButton) {
//         printButton.addEventListener('click', printPDF);
//     }
// });
