// db.js
const mysql = require("mysql2");

const db = mysql.createConnection({
  host: "localhost", // change to your MySQL host
  user: "root",      // change to your MySQL username
  password: "",      // change to your MySQL password
  database: "bus_tickets_qr"
});

db.connect((err) => {
  if (err) {
    console.error("❌ Database connection failed:", err.message);
  } else {
    console.log("✅ Connected to MySQL database.");
  }
});

module.exports = db;
