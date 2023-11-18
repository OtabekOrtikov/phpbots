const fs = require('fs');

// Read the words from words.txt
fs.readFile('words.txt', 'utf8', (err, data) => {
  if (err) {
    console.error(err);
    return;
  }

  // Convert the words to capitalized form
  const capitalizedWords = data.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1));

  // Save the capitalized words to capwords.txt
  fs.writeFile('capwords.txt', capitalizedWords.join(' '), 'utf8', err => {
    if (err) {
      console.error(err);
      return;
    }
    console.log('Capitalized words saved to capwords.txt');
  });
});
