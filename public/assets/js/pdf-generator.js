// public/assets/js/pdf-generator.js

function generateTablePDF(title, headers, rows, options = {}) {
    // Prepare table body: first row is headers
    const bodyData = [
        headers.map(header => ({ text: header, style: 'tableHeader' }))
    ];

    // Add rows data
    rows.forEach(row => {
        const rowData = row.map(cell => ({ text: String(cell), style: 'tableCell' }));
        bodyData.push(rowData);
    });

    // PDF document definition
    const docDefinition = {
        header: (currentPage, pageCount, pageSize) => {
            return {
                image: pdfImages.header,
                width: pageSize.width - 80,
                alignment: 'center',
                margin: [40, 10, 40, 20]
            };
        },
        footer: (currentPage, pageCount, pageSize) => {
            return {
                columns: [{
                        image: pdfImages.footer,
                        width: pageSize.width - 120,
                        alignment: 'center',
                        margin: [60, 0, 60, 10]
                    },
                    {
                        text: `Page ${currentPage} of ${pageCount}`,
                        alignment: 'right',
                        margin: [0, 0, 40, 10],
                        fontSize: 8,
                        color: '#888'
                    }
                ]
            };
        },
        content: [
            { text: title || 'Document Title', style: 'title' },
            {
                table: {
                    headerRows: 1,
                    widths: options.columnWidths || Array(headers.length).fill('*'),
                    body: bodyData
                },
                layout: options.layout || 'lightHorizontalLines'
            }
        ],
        styles: {
            title: {
                fontSize: 18,
                bold: true,
                alignment: 'center',
                margin: [0, 0, 0, 20]
            },
            tableHeader: {
                bold: true,
                fillColor: '#343a40',
                color: 'white',
                fontSize: 11,
                margin: [0, 5, 0, 5]
            },
            tableCell: {
                fontSize: 10,
                margin: [0, 3, 0, 3]
            }
        },
        defaultStyle: {
            font: 'Helvetica'
        }
    };

    pdfMake.createPdf(docDefinition).download(`${title || 'document'}.pdf`);
}