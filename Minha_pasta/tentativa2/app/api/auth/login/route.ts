import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function POST(req: NextRequest) {
  try {
    const { username, password } = await req.json()

    const results: any = await query("SELECT id, username, password FROM users WHERE username = ?", [username])

    if (results.length === 0) {
      return NextResponse.json({ error: "Usuário não encontrado" }, { status: 401 })
    }

    const user = results[0]

    // Para produção, use bcrypt para comparar senhas
    // Por enquanto, comparação direta para prototipagem
    if (user.password !== password) {
      return NextResponse.json({ error: "Senha incorreta" }, { status: 401 })
    }

    return NextResponse.json({
      id: user.id,
      username: user.username,
    })
  } catch (error) {
    console.error("Login error:", error)
    return NextResponse.json({ error: "Erro ao fazer login" }, { status: 500 })
  }
}
