"use client"

import type React from "react"
import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Eye, EyeOff } from "lucide-react"
import Image from "next/image"

interface LoginProps {
  onLogin: (username: string) => void
  onToggleSignUp: () => void
}

export default function Login({ onLogin, onToggleSignUp }: LoginProps) {
  const [username, setUsername] = useState("")
  const [password, setPassword] = useState("")
  const [error, setError] = useState("")
  const [loading, setLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError("")
    setLoading(true)

    if (!username || !password) {
      setError("Por favor, preencha todos os campos")
      setLoading(false)
      return
    }

    try {
      const response = await fetch("http://localhost/tentativa2/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      })

      const data = await response.json()

      if (data.success) {
        localStorage.setItem("userId", data.userId)
        localStorage.setItem("username", username)
        onLogin(username)
      } else {
        setError(data.message || "Usuário ou senha incorretos")
      }
    } catch (err) {
      setError("Erro ao conectar com o servidor")
      console.error("[v0] Login error:", err)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-300 to-blue-200 p-4">
      <Card className="w-full max-w-md shadow-xl">
        <CardHeader className="space-y-2 text-center">
          <div className="w-16 h-16 mx-auto mb-2">
            <Image
              src="/logo-flametalk.png"
              alt="FlameTalk Logo"
              width={64}
              height={64}
              priority
              className="w-full h-full object-contain"
            />
          </div>
          <CardTitle className="text-3xl">FlameTalk</CardTitle>
          <CardDescription>Entre com sua conta</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <label className="text-sm font-medium">Usuário</label>
              <Input
                type="text"
                placeholder="Digite seu usuário"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                className="rounded-xl"
                disabled={loading}
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium">Senha</label>
              <div className="relative">
                <Input
                  type={showPassword ? "text" : "password"}
                  placeholder="Digite sua senha"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="rounded-xl pr-10"
                  disabled={loading}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  disabled={loading}
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>
            {error && <div className="text-sm text-red-600 bg-red-50 p-3 rounded-lg">{error}</div>}
            <Button type="submit" className="w-full rounded-xl h-10 bg-blue-600 hover:bg-blue-700" disabled={loading}>
              {loading ? "Entrando..." : "Entrar"}
            </Button>
          </form>

          <div className="mt-6 pt-6 border-t text-center">
            <p className="text-sm text-muted-foreground mb-3">Não tem uma conta?</p>
            <Button
              type="button"
              variant="outline"
              onClick={onToggleSignUp}
              className="w-full rounded-xl bg-transparent"
              disabled={loading}
            >
              Cadastre-se já
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
