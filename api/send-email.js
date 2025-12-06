import nodemailer from "nodemailer";

export default async function handler(req, res) {
  if (req.method !== "POST") {
    return res.status(405).json({ message: "Only POST requests allowed" });
  }

  const { name, email, subject, message } = req.body;

  // Input validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!name || typeof name !== 'string' || name.trim().length < 2) {
    return res.status(400).json({ message: "Name must be at least 2 characters" });
  }

  if (!email || typeof email !== 'string' || !emailRegex.test(email)) {
    return res.status(400).json({ message: "Invalid email address" });
  }

  if (!subject || typeof subject !== 'string' || subject.trim().length < 3) {
    return res.status(400).json({ message: "Subject must be at least 3 characters" });
  }

  if (!message || typeof message !== 'string' || message.trim().length < 10) {
    return res.status(400).json({ message: "Message must be at least 10 characters" });
  }

  if (message.length > 5000) {
    return res.status(400).json({ message: "Message is too long (max 5000 characters)" });
  }

  // Sanitize inputs
  const sanitized = {
    name: name.trim().substring(0, 100).replace(/[<>]/g, ''),
    email: email.trim().toLowerCase().substring(0, 100),
    subject: subject.trim().substring(0, 200).replace(/[<>]/g, ''),
    message: message.trim().substring(0, 5000).replace(/[<>]/g, '')
  };

  // Simple rate limiting check (basic - could use Redis in production)
  // Note: In production, implement proper rate limiting with Redis or similar

  let transporter = nodemailer.createTransport({
    service: "gmail",
    auth: {
      user: process.env.MY_EMAIL,
      pass: process.env.MY_PASSWORD,
    },
  });

  try {
    await transporter.sendMail({
      from: `"${sanitized.name}" <${process.env.MY_EMAIL}>`, // Use your email to prevent spoofing
      replyTo: sanitized.email, // Allow replies to original sender
      to: process.env.MY_EMAIL,
      subject: `New message: ${sanitized.subject}`,
      text: `
        Name: ${sanitized.name}
        Email: ${sanitized.email}
        Subject: ${sanitized.subject}
        Message: ${sanitized.message}
      `,
      html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
          <h2 style="color: #00f5ff;">New Contact Form Message</h2>
          <p><strong>Name:</strong> ${sanitized.name}</p>
          <p><strong>Email:</strong> ${sanitized.email}</p>
          <p><strong>Subject:</strong> ${sanitized.subject}</p>
          <hr style="border: 1px solid #ddd; margin: 20px 0;">
          <p><strong>Message:</strong></p>
          <p style="white-space: pre-wrap;">${sanitized.message.replace(/\n/g, '<br>')}</p>
        </div>
      `,
    });

    res.status(200).json({ message: "Email sent successfully!" });
  } catch (error) {
    console.error("Email send error:", error);
    res.status(500).json({ message: "Failed to send email. Please try again later." });
  }
}
