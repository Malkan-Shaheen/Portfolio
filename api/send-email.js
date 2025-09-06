import nodemailer from "nodemailer";

export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Only POST requests allowed" });
  }

  const { name, email, subject, message } = req.body;

  // Create transporter using Gmail SMTP
  let transporter = nodemailer.createTransport({
    service: "gmail",
    auth: {
      user: "malkanshaheen45@gmail.com",   // your Gmail
      pass: "smgv uydu gblb nqzj" // app password, not your real password
    }
  });

  try {
    await transporter.sendMail({
      from: email,
      to: "malkanshaheen45@gmail.com", // where you’ll receive messages
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
    res.status(500).json({ message: "Failed to send email", error });
  }
}
