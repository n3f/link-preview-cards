import { runCLI } from "@wp-playground/cli";

console.log("Starting WordPress Playground server...");

const cliServer = await runCLI({
  command: "server",
  blueprint: "./assets/blueprints/dev.json",
  autoMount: true,
  port: 8888,
});

console.log("✅ WordPress Playground server started successfully!");

// Handle graceful shutdown
process.on('SIGINT', async () => {
  console.log('\n🛑 Shutting down server...');
  await cliServer[Symbol.asyncDispose]();
  process.exit(0);
});

process.on('SIGTERM', async () => {
  console.log('\n🛑 Shutting down server...');
  await cliServer[Symbol.asyncDispose]();
  process.exit(0);
});
