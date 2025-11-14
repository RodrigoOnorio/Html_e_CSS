"use client"

import { useEffect, useState } from "react"
import Login from "@/components/auth/login"
import SignUp from "@/components/auth/signup"
import Chat from "@/components/chat/chat"

export default function Home() {
  const [user, setUser] = useState<string | null>(null)
  const [isSignUp, setIsSignUp] = useState(false)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const storedUsername = localStorage.getItem("username")
    const storedUserId = localStorage.getItem("userId")

    if (storedUsername && storedUserId) {
      setUser(storedUsername)
    }
    setLoading(false)
  }, [])

  const handleLogin = (username: string) => {
    setUser(username)
    setIsSignUp(false)
  }

  const handleSignUp = (username: string) => {
    setUser(username)
    setIsSignUp(false)
  }

  const handleLogout = async () => {
    try {
      await fetch("/app/api/auth/logout.php", { method: "POST" })
    } catch (err) {
      console.error("[v0] Logout error:", err)
    }

    setUser(null)
    localStorage.removeItem("username")
    localStorage.removeItem("userId")
    localStorage.removeItem("messages")
    setIsSignUp(false)
  }

  if (loading) {
    return <div className="flex items-center justify-center h-screen bg-background">Carregando...</div>
  }

  if (!user) {
    return isSignUp ? (
      <SignUp onSignUp={handleSignUp} onToggleLogin={() => setIsSignUp(false)} />
    ) : (
      <Login onLogin={handleLogin} onToggleSignUp={() => setIsSignUp(true)} />
    )
  }

  return <Chat user={user} onLogout={handleLogout} />
}
