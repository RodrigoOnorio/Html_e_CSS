import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function GET(req: NextRequest, { params }: { params: { id: string } }) {
  try {
    const fileId = params.id

    const files: any = await query("SELECT file_data, file_type FROM files WHERE id = ?", [fileId])

    if (files.length === 0) {
      return NextResponse.json({ error: "Arquivo não encontrado" }, { status: 404 })
    }

    const file = files[0]
    const buffer = Buffer.from(file.file_data, "base64")

    return new NextResponse(buffer, {
      headers: {
        "Content-Type": file.file_type,
        "Content-Disposition": "inline",
      },
    })
  } catch (error) {
    console.error("File retrieval error:", error)
    return NextResponse.json({ error: "Erro ao recuperar arquivo" }, { status: 500 })
  }
}
