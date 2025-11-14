import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function GET(req: NextRequest) {
  try {
    const currentUser = req.headers.get("x-user")

    const messages: any = await query(
      `SELECT * FROM messages 
       WHERE is_deleted_for_all = 0 
       ORDER BY created_at ASC`,
    )

    const filteredMessages = messages.map((msg: any) => {
      const deletedForMe = msg.deleted_for_me ? msg.deleted_for_me.split(",") : []
      const isDeletedForMe = deletedForMe.includes(currentUser)

      return {
        id: msg.id,
        sender: msg.sender,
        content: msg.content,
        fileUrl: msg.file_url,
        fileType: msg.file_type,
        createdAt: msg.created_at,
        editedAt: msg.edited_at,
        isEdited: !!msg.edited_at,
        isDeletedForMe,
        isDeletedForAll: msg.is_deleted_for_all,
      }
    })

    return NextResponse.json(filteredMessages)
  } catch (error) {
    console.error("Get messages error:", error)
    return NextResponse.json({ error: "Erro ao buscar mensagens" }, { status: 500 })
  }
}
