const express = require("express");
const app = express();
const MongoClient = require("mongodb").MongoClient;

const uri =
  "mongodb+srv://xuandy2003:Ilovemonicamimi2003@cluster0.eoymx0c.mongodb.net/?retryWrites=true&w=majority";
const client = new MongoClient(uri, {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

app.use(express.urlencoded({ extended: true }));

app.get("/", (req, res) => {
  res.sendFile(__dirname + "/stockTicker_form.html");
});

app.post("/process", async (req, res) => {
  const inputType = req.body["input-type"]; // get value of radio button
  const inputField = req.body["input-field"]; // get value of input field

  try {
    await client.connect(); // connect to the MongoDB cluster
    const database = client.db("companiesList"); // select database
    const collection = database.collection("companies"); // select collection
    let query = {};
    if (inputType === "symbol") {
      query = { ticker: inputField.toUpperCase() }; // look up the company by its stock ticker symbol
    } else {
      query = { name: inputField }; // look up the company by its name
    }
    const companies = await collection.find(query).toArray(); // execute query
    if (companies.length > 0) {
      let output = "";
      for (let i = 0; i < companies.length; i++) {
        output += `<b>Company</b>: ${companies[i].name} - <b>Ticker Symbol</b>: ${companies[i].ticker} - <b>Price</b>: $${companies[i].price}<br>`; // display data
      }
      res.send(output);
    } else {
      res.send("Company not found."); // if company not found
    }
  } catch (err) {
    console.error(err);
  } finally {
    await client.close(); // close connection to MongoDB cluster
  }
});

app.listen(3000, () => {
  console.log("Server started on port 3000");
});
