let text = '';
let sum = 0.0;
const totalAnswer = chart.data.datasets[0].data.reduce((pv, cv) => pv + parseInt(cv, 10), 0);
const averageInput = jQuery('.average-linear-' + chart.canvas.id);

for (let i = 0; i < chart.data.datasets[0].data.length; i++) {
    sum += parseInt(chart.data.labels[i], 10) * parseInt(chart.data.datasets[0].data[i], 10);
    text += `
    <tr>
        <td width="16">
            <span style="display:block;width:16px;height:16px;background-color:${chart.data.datasets[0].backgroundColor[i]}"></span>
        </td>
        <td>
            ${chart.data.labels[i]}
        </td>
        <td>
            ${chart.data.datasets[0].data[i]}
        </td>
        <td>
            ${Math.round(chart.data.datasets[0].data[i] / totalAnswer * 10000) / 100}&nbsp;%
        </td>
    </tr>
`
}

text += `
    <tr>
        <td></td>
        <td>
            <span class="font-weight-bold">Total</span>
        </td>
        <td>
            ${totalAnswer}
        </td>
        <td>
            100&nbsp;%
        </td>
    </tr>
`;

if (averageInput !== undefined) {
    averageInput.html(Math.round(sum / totalAnswer * 100) / 100);
}

return text;
