import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function DELETE(req: NextRequest) {
  try {
    const { messageId, username, deleteForAll } = await req.json()

    if (deleteForAll) {
      await query("UPDATE messages SET is_deleted_for_all = 1 WHERE id = ?", [messageId])
    } else {
      const messages: any = await query("SELECT deleted_for_me FROM messages WHERE id = ?", [messageId])

      let deletedList = messages[0].deleted_for_me || ""
      deletedList = deletedList ? `${deletedList},${username}` : username

      await query("UPDATE messages SET deleted_for_me = ? WHERE id = ?", [deletedList, messageId])
    }

    return NextResponse.json({
      message: "Mensagem deletada com sucesso",
    })
  } catch (error) {
    console.error("Delete message error:", error)
    return NextResponse.json({ error: "Erro ao deletar mensagem" }, { status: 500 })
  }
}
