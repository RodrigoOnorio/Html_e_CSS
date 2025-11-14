import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function POST(req: NextRequest) {
  try {
    const formData = await req.formData()
    const file = formData.get("file") as File
    const username = formData.get("username") as string

    if (!file) {
      return NextResponse.json({ error: "Arquivo não fornecido" }, { status: 400 })
    }

    const bytes = await file.arrayBuffer()
    const buffer = Buffer.from(bytes)
    const base64 = buffer.toString("base64")

    const fileType = file.type
    const fileName = file.name

    const result: any = await query(
      "INSERT INTO files (filename, file_data, file_type, uploaded_by, created_at) VALUES (?, ?, ?, ?, NOW())",
      [fileName, base64, fileType, username],
    )

    return NextResponse.json({
      fileId: result.insertId,
      fileName,
      fileType,
      fileUrl: `/api/files/${result.insertId}`,
    })
  } catch (error) {
    console.error("Upload error:", error)
    return NextResponse.json({ error: "Erro ao fazer upload" }, { status: 500 })
  }
}
