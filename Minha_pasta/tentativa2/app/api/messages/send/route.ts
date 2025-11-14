import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function POST(req: NextRequest) {
  try {
    const { username, content, fileUrl, fileType } = await req.json()

    const result: any = await query(
      "INSERT INTO messages (sender, content, file_url, file_type, created_at) VALUES (?, ?, ?, ?, NOW())",
      [username, content, fileUrl || null, fileType || null],
    )

    return NextResponse.json({
      id: result.insertId,
      sender: username,
      content,
      fileUrl,
      fileType,
      createdAt: new Date(),
      isEdited: false,
      isDeletedForMe: false,
      isDeletedForAll: false,
    })
  } catch (error) {
    console.error("Send message error:", error)
    return NextResponse.json({ error: "Erro ao enviar mensagem" }, { status: 500 })
  }
}
