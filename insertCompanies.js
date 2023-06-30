const fs = require("fs");
const readline = require("readline");
const { MongoClient } = require("mongodb");

const uri =
  "mongodb+srv://xuandy2003:Ilovemonicamimi2003@cluster0.eoymx0c.mongodb.net/?retryWrites=true&w=majority";

const client = new MongoClient(uri, {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

// Insert document into collection
async function insertDocument(document) {
  const client = new MongoClient(uri, {
    useNewUrlParser: true,
    useUnifiedTopology: true,
  });
  try {
    await client.connect();
    const database = client.db("companiesList");
    const collection = database.collection("companies");
    const result = await collection.insertOne(document);
    console.log(`Inserted document with _id: ${result.insertedId}`);
  } finally {
    await client.close();
  }
}

// Open file stream for reading
const stream = fs.createReadStream("companies.csv");

// Read file line by line
const rl = readline.createInterface({
  input: stream,
  crlfDelay: Infinity,
});

// Read each line of file and insert into collection
rl.on("line", (line) => {
  const [name, ticker, price] = line.split(",");

  // Create document to insert into collection
  const document = {
    name: name.trim(),
    ticker: ticker.trim(),
    price: parseFloat(price.trim()),
  };

  insertDocument(document);
});

// Handle errors 
stream.on("error", (error) => console.error(`Error reading file: ${error}`));
client.on("error", (error) =>
  console.error(`Error connecting to MongoDB: ${error}`)
);
