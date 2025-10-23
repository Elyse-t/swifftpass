// server.js
const express = require("express");
const QRCode = require("qrcode");
const { v4: uuidv4 } = require("uuid");
const path = require("path");
const db = require("./db");

const app = express();
app.use(express.json());
app.use(express.static(path.join(__dirname, "public")));

// 🟢 Generate QR
app.post("/generate", async (req, res) => {
  const { user, numberOfTickets } = req.body;
  if (!user || !numberOfTickets) {
    return res.status(400).json({ message: "User and numberOfTickets required" });
  }

  const id = uuidv4();

  db.query(
    "INSERT INTO tickets (id, user, numberOfTickets) VALUES (?, ?, ?)",
    [id, user, numberOfTickets],
    async (err) => {
      if (err) return res.status(500).json({ error: err.message });

      const qrData = `http://localhost:3000/scan/${id}`;
      const qrImage = await QRCode.toDataURL(qrData);

      res.json({ message: "QR code generated successfully", id, qrImage });
    }
  );
});

// 🟡 Scan Ticket
app.get("/scan/:id", (req, res) => {
  const { id } = req.params;
  db.query("SELECT * FROM tickets WHERE id = ?", [id], (err, result) => {
    if (err) return res.status(500).json({ error: err.message });
    if (result.length === 0) return res.status(404).json({ message: "Invalid QR code" });

    const ticket = result[0];
    if (ticket.used) {
      return res.send(`
        <h2 style="color:red;">❌ This ticket has already been used!</h2>
        <a href="/scan.html">Back</a>
      `);
    }

    if (ticket.numberOfTickets <= 0) {
      return res.send(`
        <h2 style="color:orange;">⚠️ No tickets remaining!</h2>
        <a href="/scan.html">Back</a>
      `);
    }

    const remaining = ticket.numberOfTickets - 1;
    const used = remaining === 0 ? 1 : 0;

    db.query(
      "UPDATE tickets SET numberOfTickets = ?, used = ? WHERE id = ?",
      [remaining, used, id],
      (err2) => {
        if (err2) return res.status(500).json({ error: err2.message });

        res.send(`
          <h2 style="color:green;">✅ Ticket scanned successfully!</h2>
          <p>Remaining Tickets: ${remaining}</p>
          <a href="/scan.html">Back</a>
        `);
      }
    );
  });
});

app.listen(3000, () =>
  console.log("🚍 Server running on http://localhost:3000")
);
