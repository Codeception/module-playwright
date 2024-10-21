#!/usr/bin/env node
const express = require('express');
const Playwright = require('codeceptjs/lib/helper/Playwright');
const path = require('path');
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

const tests = {};

console.log('Playwright server started, waiting for init command');

app.post('/init', async (req, res) => {
  const { arguments } = req.body;

  // where we store artifacts
  global.output_dir = arguments.output_dir || './tests/_output';
  global.codecept_dir = arguments.data_dir || './tests/_data';
  delete arguments.output_dir;
  delete arguments.data_dir;

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

app.get('/test/:id', async (req, res) => {
  const id = req.params.id; // Accessing the id value from the URL
  res.status(200).json(tests[id]);
});

app.post('/hook', async (req, res) => {
  const { command, arguments } = req.body;

  const hook = command;
  const { id, title } = arguments;

  try {
    let result;
    switch (hook) {
      case 'beforeSuite':
      case 'afterSuite':
        result = await playwright[`_${hook}`]();
        break;
      case 'before':
        if (!tests[id]) tests[id] = { title }
      case 'failed':
        const fileName = `${id}_failed.png`;
        try {
          await playwright.saveScreenshot(fileName);
          if (!tests[id]) tests[id] = { title }
          if (!tests[id].artifacts) tests[id].artifacts = {}
          tests[id].artifacts.screenshot = path.join(output_dir, fileName);
        } catch (err) {
          console.error('Error saving screenshot: ', err);
          // not matter
        }
      default:
        result = await playwright[`_${hook}`](tests[id]);
    }

    const test = tests[id];
    res.status(200).json({ result, test });
  } catch (error) {
    const message = error.inspect ? error.inspect() : error.message;
    res.status(500).json({ message });
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
