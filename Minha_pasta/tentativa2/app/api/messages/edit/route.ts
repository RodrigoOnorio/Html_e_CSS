import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function PUT(req: NextRequest) {
  try {
    const { messageId, content, username } = await req.json()

    const messages: any = await query("SELECT created_at FROM messages WHERE id = ?", [messageId])

    if (messages.length === 0) {
      return NextResponse.json({ error: "Mensagem não encontrada" }, { status: 404 })
    }

    const createdAt = new Date(messages[0].created_at)
    const now = new Date()
    const diffMinutes = (now.getTime() - createdAt.getTime()) / (1000 * 60)

    if (diffMinutes > 15) {
      return NextResponse.json({ error: "Você só pode editar mensagens em até 15 minutos" }, { status: 400 })
    }

    await query("UPDATE messages SET content = ?, edited_at = NOW() WHERE id = ?", [content, messageId])

    return NextResponse.json({
      message: "Mensagem editada com sucesso",
    })
  } catch (error) {
    console.error("Edit message error:", error)
    return NextResponse.json({ error: "Erro ao editar mensagem" }, { status: 500 })
  }
}
