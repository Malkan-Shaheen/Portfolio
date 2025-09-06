import nodemailer from "nodemailer";

export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Only POST requests allowed" });
  }

  const { name, email, subject, message } = req.body;

  let transporter = nodemailer.createTransport({
    service: "gmail",
    auth: {
      user: process.env.MY_EMAIL,     // from Vercel env vars
      pass: process.env.MY_PASSWORD,  // Gmail app password
    },
  });

  try {
    await transporter.sendMail({
      from: email,
      to: process.env.MY_EMAIL, // you receive emails here
      subject: `New message: ${subject}`,
      text: `
        Name: ${name}
        Email: ${email}
        Message: ${message}
      `,
    });

    res.status(200).json({ message: "Email sent successfully!" });
  } catch (error) {
    console.error("Email send error:", error);
    res.status(500).json({ message: "Failed to send email", error: error.message }); // ✅ return only message
  }
}
