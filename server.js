const express = require('express');
const Playwright = require('codeceptjs/lib/helper/Playwright');
const app = express();
const port = 8191;
app.use(express.json());

process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});

process.on('uncaughtException', (error) => {
  console.error('Uncaught Exception:', error);
});

let playwright;

console.log('Playwright server started, waiting for init command');

app.post('/init', async (req, res) => {
  const { arguments } = req.body;

  // where we store artifacts
  global.output_dir = arguments.output_dir || './output';
  delete arguments.output_dir;

  delete arguments.pw_server;

  console.log('Init: ', arguments);

  try {
    playwright = new Playwright(arguments);
    await playwright._init();
    res.status(200).json({ message: 'Playwright initialized successfully' });
  } catch(e) {
    console.error('Error initializing Playwright: ', e.message);
    res.status(400).json({ message: e.message });
  }
});

app.post('/command', async (req, res) => {
  const { command, arguments } = req.body;
  console.log('Command: ', command, arguments);

  if (!playwright) {
    console.error('Playwright is not initialized!');
    return res.status(400).json({ message: 'Playwright is not initialized' });
  }

  if (!command || !Array.isArray(arguments)) {
    return res.status(400).json({ message: 'Invalid request body' });
  }

  try {
    const result = await playwright[command](...arguments);
    res.status(200).json({ result });
  } catch (error) {
    const message = error.inspect ? error.inspect() : error.message;
    console.error(`Error executing command ${command}:`, message);
    res.status(500).json({ message });
  }
});

app.listen(port, () => {
  console.log(`Server listening at http://localhost:${port}`);
});
