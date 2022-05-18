const sharp = require('sharp');
const fs = require('fs');
const directory = './uploads';
const newDir = './new';

fs.readdirSync(directory).forEach(file => {
    const extension = file.split('.')[1]; // gets the extension
    const new_file= file.split('.')[0]; // gets the extension
    sharp(`${directory}/${file}`)
        .resize(320, 362) // width, height
        .toFile(`${newDir}/${new_file}.${extension}`);
});
