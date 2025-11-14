"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { LogOut, Settings } from "lucide-react"
import SettingsMenu from "./settings-menu"

interface ChatHeaderProps {
  user: string
  onLogout: () => void
  isDark: boolean
  onToggleDark: () => void
}

export default function ChatHeader({ user, onLogout, isDark, onToggleDark }: ChatHeaderProps) {
  const [showSettings, setShowSettings] = useState(false)

  return (
    <header className="bg-primary text-primary-foreground shadow-md">
      <div className="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <img src="/logo-flametalk.png" alt="FlameTalk" className="w-8 h-8" />
          <div>
            <h1 className="text-xl font-bold">FlameTalk</h1>
            <p className="text-sm opacity-90">Olá, {user}</p>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <div className="relative">
            <Button
              variant="ghost"
              size="sm"
              onClick={() => setShowSettings(!showSettings)}
              className="text-primary-foreground hover:bg-primary/90"
            >
              <Settings className="w-5 h-5" />
            </Button>
            {showSettings && (
              <SettingsMenu
                isDark={isDark}
                onToggleDark={onToggleDark}
                onLogout={onLogout}
                onClose={() => setShowSettings(false)}
              />
            )}
          </div>
          <Button variant="ghost" size="sm" onClick={onLogout} className="text-primary-foreground hover:bg-primary/90">
            <LogOut className="w-5 h-5" />
          </Button>
        </div>
      </div>
    </header>
  )
}
